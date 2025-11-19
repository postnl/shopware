import template from './postnl-icon.html.twig';
import './postnl-icon.scss';

export default {
    template,

    data() {
        return {
            iconSvgData: '',
        };
    },

    props: {
        color: {
            type: String,
            required: false,
            default: null,
        },
        small: {
            type: Boolean,
            required: false,
            default: false,
        },
        large: {
            type: Boolean,
            required: false,
            default: false,
        },
        size: {
            type: String,
            required: false,
            default: null,
        },
        decorative: {
            type: Boolean,
            required: false,
            default: false,
        },
    },

    computed: {
        classes() {
            return [
                {
                    'postnl-icon--small': this.small,
                    'postnl-icon--large': this.large,
                },
            ];
        },

        styles() {
            let size = this.size;

            if (!Number.isNaN(parseFloat(size)) && !Number.isNaN(size - 0)) {
                size = `${size}px`;
            }

            return {
                color: this.color,
                width: size,
                height: size,
            };
        },
    },

    beforeMount() {
        this.iconSvgData = `<svg id="postnl-icon"></svg>`;
    },

    mounted() {
        this.loadIconSvgData()
    },

    methods: {
        loadIconSvgData() {
            // eslint-disable-next-line max-len
            return import(`./../../../assets/icons/postnl.svg?raw`)
                .then((iconSvgData) => {
                    if (iconSvgData.default) {
                        this.iconSvgData = iconSvgData.default;
                    } else {
                        // note this only happens if the import exists but does not export a default
                        console.error(`The SVG file for the PostNL could not be found and loaded.`);
                        this.iconSvgData = '';
                    }
                });
        },
    },
};
