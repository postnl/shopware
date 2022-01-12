import template from './memo-collapse.html.twig';
import './memo-collapse.scss';

const { Component } = Shopware;

Component.extend('memo-collapse', 'sw-collapse', {
    template,

    props: {
        disabled: {
            type: Boolean,
            required: false,
            default: false
        },
    },

    computed: {
        headerClass() {
            return {
                'is--disabled': this.disabled,
            };
        },
        expandButtonClass() {
            return {
                'is--hidden': this.disabled || this.expanded,
            };
        },
        collapseButtonClass() {
            return {
                'is--hidden': this.disabled || !this.expanded,
            };
        },
    },

    methods: {
        collapseItem() {
            if(this.disabled) {
                return;
            }
            this.expanded = !this.expanded;
        },
    },
});
