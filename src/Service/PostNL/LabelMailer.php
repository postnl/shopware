<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Service\PostNL;

use PostNL\Shopware6\Service\PostNL\Label\Label;
use PostNL\Shopware6\Service\PostNL\Label\LabelInterface;
use Psr\Log\LoggerInterface;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Content\Mail\Service\AbstractMailService;
use Shopware\Core\Content\MailTemplate\MailTemplateEntity;
use Shopware\Core\Framework\Adapter\Translation\AbstractTranslator;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Validation\DataBag\DataBag;
use Shopware\Core\System\Locale\LanguageLocaleCodeProvider;

class LabelMailer
{
    protected AbstractMailService        $mailService;
    protected EntityRepository           $mailTemplateRepository;
    protected AbstractTranslator         $translator;
    protected LanguageLocaleCodeProvider $languageLocaleCodeProvider;
    protected LoggerInterface            $logger;

    public function __construct(
        AbstractMailService        $mailService,
        EntityRepository           $mailTemplateRepository,
        AbstractTranslator         $translator,
        LanguageLocaleCodeProvider $languageLocaleCodeProvider,
        LoggerInterface            $logger
    )
    {
        $this->mailService = $mailService;
        $this->mailTemplateRepository = $mailTemplateRepository;
        $this->translator = $translator;
        $this->languageLocaleCodeProvider = $languageLocaleCodeProvider;
        $this->logger = $logger;
    }

    /**
     * @param OrderEntity      $order
     * @param LabelInterface[] $labels
     * @param string           $mailTemplateId
     * @param Context          $context
     * @return void
     */
    public function send(
        OrderEntity $order,
        array       $labels,
        string      $mailTemplateId,
        Context     $context
    ): void
    {
        $labelContext = new Context(
            $context->getSource(),
            $context->getRuleIds(),
            $context->getCurrencyId(),
            [$order->getLanguageId(), ...$context->getLanguageIdChain()],
            $context->getVersionId(),
            $context->getCurrencyFactor(),
            $context->considerInheritance()
        );

        $injectedTranslator = $this->injectTranslator($labelContext, $order->getSalesChannelId());

        $data = new DataBag();

        $mailTemplate = $this->getMailTemplate($mailTemplateId, $labelContext);
        $recipients = $this->getRecipients($order);

        $data->set('recipients', $recipients);
        $data->set('senderName', $mailTemplate->getTranslation('senderName'));
        $data->set('salesChannelId', $order->getSalesChannelId());

        $data->set('templateId', $mailTemplate->getId());
        $data->set('customFields', $mailTemplate->getCustomFields());
        $data->set('contentHtml', $mailTemplate->getTranslation('contentHtml'));
        $data->set('contentPlain', $mailTemplate->getTranslation('contentPlain'));
        $data->set('subject', $mailTemplate->getTranslation('subject'));
        $data->set('mediaIds', []);

        $templateData = [
            'order'        => $order->jsonSerialize(),
            'salesChannel' => $order->getSalesChannel()->jsonSerialize(),
        ];

        $binAttachments = [];
        foreach ($labels as $label) {
            $binAttachments[] = [
                'content'  => base64_decode($label->getContent()),
                'fileName' => sprintf(
                    '%s-%s.pdf',
                    $label instanceof Label ? $label->getBarcode() : $order->getOrderNumber(),
                    $label->getType()
                ),
                'mimeType' => 'application/pdf',
            ];
        }

        $data->set('binAttachments', $binAttachments);

        try {
            $this->mailService->send(
                $data->all(),
                $labelContext,
                $templateData
            );
        }
        catch (\Exception $e) {
            $this->logger->error(
                "Could not send mail:\n"
                . $e->getMessage() . "\n"
                . 'Error Code:' . $e->getCode() . "\n"
                . "Template data: \n"
                . json_encode($data->all(), \JSON_THROW_ON_ERROR) . "\n"
            );
        }

        if ($injectedTranslator) {
            $this->translator->resetInjection();
        }
    }

    private function getMailTemplate(string $id, Context $context): ?MailTemplateEntity
    {
        $criteria = new Criteria([$id]);
        $criteria->setTitle('send-mail::load-mail-template');
        $criteria->addAssociation('media.media');
        $criteria->setLimit(1);

        /** @var ?MailTemplateEntity $mailTemplate */
        $mailTemplate = $this->mailTemplateRepository
            ->search($criteria, $context)
            ->first();

        return $mailTemplate;
    }

    private function getRecipients(OrderEntity $order): array
    {
        $customer = $order->getOrderCustomer();
        return [$customer->getEmail() => $customer->getFirstName() . ' ' . $customer->getLastName()];
    }

    private function injectTranslator(Context $context, ?string $salesChannelId): bool
    {
        if ($salesChannelId === null) {
            return false;
        }

        if ($this->translator->getSnippetSetId() !== null) {
            return false;
        }

        $this->translator->injectSettings(
            $salesChannelId,
            $context->getLanguageId(),
            $this->languageLocaleCodeProvider->getLocaleForLanguageId($context->getLanguageId()),
            $context
        );

        return true;
    }
}