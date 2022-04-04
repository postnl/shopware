
// TODO create initializer like Shopware
Shopware.Component.register('icons-postnl', {
    functional: true,

    render(createElement, elementContext) {
        const data = elementContext.data;

        return createElement('span', {
            class: [data.staticClass, data.class],
            style: data.style,
            attrs: data.attrs,
            on: data.on,
            domProps: {
                innerHTML: '<svg xmlns="http://www.w3.org/2000/svg" width="2500" height="2471" viewBox="118.293 68.6 185.459 183.296"><g fill="#313237"><path d="M240.639 95.872C216.816 82.737 181.029 68.6 158.754 68.6c-5.844 0-10.551.963-13.988 2.854-8.32 4.59-15.277 16.968-20.131 35.81-4.027 15.662-6.342 34.931-6.342 52.86 0 35.681 8.285 78.646 26.473 88.679 3.721 2.051 8.699 3.094 14.799 3.094 23.014 0 58.055-14.824 81.074-27.517 25.354-13.979 63.074-42.622 63.113-64.256-.035-20.33-36.922-49.81-63.113-64.252m-2.993 124.024c-36.54 20.146-75.346 31.727-89.596 23.881-15.713-8.67-23.582-50.099-23.586-83.1.004-32.745 8.357-74.694 23.586-83.095 13.367-7.373 53.942 4.217 89.596 23.874 30.899 17.041 59.906 43.775 59.932 59.221-.029 16.192-28.545 41.914-59.932 59.219"/><path d="M255.578 150.145c-1.506 0-3.348.377-5.072 1.032-.793.3-1.961.962-2.727 1.553l-.209.161c-.016.004-.07.033-.117.033a.214.214 0 0 1-.217-.212v-1.938a.162.162 0 0 0-.162-.159h-6.264a.735.735 0 0 0-.744.728v24.689c0 .082.074.153.162.153h6.842c.086 0 .162-.071.162-.153v-17.431c0-.128.102-.285.186-.337.318-.194 1.232-.754 2.387-1.423 1.16-.663 2.336-1.022 3.303-1.022 2.715 0 3.875 1.454 3.875 4.867v15.347c0 .082.068.153.154.153h6.828c.092 0 .156-.063.156-.153v-16.599c0-6.079-2.953-9.289-8.543-9.289M275.297 141.806c-1.232-1.359-5.041-3.756-5.473-3.756-.182 0-.217.042-.217.177l.008 37.805c0 .082.066.153.15.153h6.817a.16.16 0 0 0 .157-.153l-.002-30.463a5.625 5.625 0 0 0-1.44-3.763M180.68 150.038c-7.402 0-13.359 4.107-13.359 13.356 0 9.037 5.957 13.354 13.359 13.354 7.375 0 13.344-4.317 13.344-13.354-.001-9.242-5.969-13.356-13.344-13.356m0 20.357c-3.305 0-5.785-2.513-5.785-6.998 0-4.88 2.48-7.015 5.785-7.015 3.293 0 5.77 2.134 5.77 7.015-.001 4.485-2.477 6.998-5.77 6.998M235.461 170.166c-.248 0-1.934.66-3.01.66-2.289 0-3.658-1.08-3.658-4.704v-9.438c0-.135.107-.248.248-.248h6.184a.243.243 0 0 0 .248-.249h.004l-.004-4.858a.812.812 0 0 0-.809-.808h-5.623a.243.243 0 0 1-.248-.246v-6.682c0-.204-.09-.264-.305-.264-.496 0-4.346 2.472-5.541 3.785a5.753 5.753 0 0 0-1.465 3.822v16.417c0 7.123 4.113 9.35 8.539 9.35 2.531 0 4.25-.372 5.27-.922a.813.813 0 0 0 .422-.715v-4.651a.252.252 0 0 0-.252-.249M150.023 150.521h-9.734a.824.824 0 0 0-.832.82v29.888a5.77 5.77 0 0 0 1.465 3.824c1.203 1.318 5.051 3.785 5.551 3.785.215 0 .301-.058.301-.265V176.53c0-.143.107-.253.25-.253h2.846c9.127 0 14.672-5.15 14.672-12.586-.001-7.426-4.954-13.17-14.519-13.17m0 19.066h-3.004a.247.247 0 0 1-.246-.249v-11.886c0-.135.107-.249.246-.249h2.857c5.396 0 7.443 2.962 7.443 6.123.001 1.792-.866 6.261-7.296 6.261M214.611 162.27c-1.512-.992-3.355-1.396-5.139-1.782-.318-.074-1.504-.339-1.771-.401-2.178-.482-3.897-.865-3.897-2.277 0-1.219 1.178-2.014 2.99-2.014 2.297 0 5.482.461 8.965 1.765.174.064.414-.038.426-.272v-5.047a.855.855 0 0 0-.6-.805c-1.217-.386-4.838-1.397-7.902-1.397-3.404 0-6.242.76-8.199 2.21-1.982 1.446-3.029 3.537-3.029 6.05 0 5.672 4.648 6.748 9.041 7.787.676.158.57.134.828.188 2.006.438 4.084.899 4.084 2.551 0 .497-.184.9-.559 1.279-.562.557-1.424.805-3.078.805-2.803 0-8.061-1.309-9.688-1.826a.4.4 0 0 0-.107-.02.302.302 0 0 0-.311.3v5.057a.83.83 0 0 0 .562.787c.035.019 4.641 1.543 8.986 1.543 7.604 0 11.5-2.976 11.5-8.401 0-2.728-1.02-4.709-3.102-6.08M180.248 117.238c.154.113.289.169.424.169h.008a.695.695 0 0 0 .422-.169 16.629 16.629 0 0 0 2.795-2.816c.133-.18.246-.401.004-.662a15.482 15.482 0 0 0-2.955-2.932c-.125-.076-.197-.093-.273-.093-.068 0-.135.017-.262.093a15.836 15.836 0 0 0-2.967 2.932c-.268.3-.07.539.012.662a16.466 16.466 0 0 0 2.792 2.816M168.533 140.146v2.769c0 .718.262 1.021.867 1.021h22.568c.613 0 .875-.303.875-1.021v-2.769a1.17 1.17 0 0 0-1.158-1.152h-21.998a1.17 1.17 0 0 0-1.154 1.152M179.127 136.176h3.098c.543 0 .58-.617.58-.625 0-.12.164-12.997.191-14.747 0-.13-.043-.422-.219-.604a.542.542 0 0 0-.385-.146 29.933 29.933 0 0 0-1.723-.044c-.791 0-1.461.027-1.711.044a.523.523 0 0 0-.383.148c-.184.18-.221.472-.221.602.025 1.75.184 14.627.189 14.747.002.024.041.625.584.625M194.154 122.43c-.822-.3-1.629-.574-2.395-.802-.98-.305-1.916-.521-2.734-.715l-.02-.003a2.225 2.225 0 0 0-.479-.062c-.82 0-1.203.576-1.357.924-.893 1.976-1.535 3.772-2.158 6.014-.051.186-.041.32.037.413.066.09.191.14.373.14h2.422c.322 0 .506-.115.574-.343.398-1.35.68-2.14 1.193-3.281.061-.115.199-.235.438-.235.078 0 .154.01.248.038l1.238.407c.465.172.633.794.475 1.235-.025.069-2.49 6.965-3.225 9.235-.082.271-.07.477.021.604.084.113.244.175.477.175h2.994c.381 0 .602-.133.707-.417.107-.295.264-.728.447-1.238.869-2.419 2.32-6.464 2.615-7.565.486-1.809.586-3.597-1.891-4.524M168.357 135.758c.104.283.332.418.717.418h2.994c.227 0 .389-.062.471-.176.094-.127.1-.333.014-.604-.728-2.27-3.191-9.166-3.221-9.235-.156-.442.014-1.062.477-1.236l1.244-.406a.846.846 0 0 1 .24-.038c.246 0 .385.12.439.235.508 1.135.805 1.932 1.195 3.282.07.227.252.342.57.342h2.426c.184 0 .311-.049.377-.139.074-.093.08-.229.027-.414-.623-2.24-1.266-4.038-2.148-6.013-.156-.35-.537-.925-1.363-.925-.15 0-.311.023-.473.062l-.019.003c-.832.195-1.764.411-2.738.715-.768.229-1.574.502-2.393.803-2.482.926-2.381 2.714-1.898 4.523.291 1.103 1.744 5.146 2.613 7.566.192.509.342.942.449 1.237"/></g></svg>',
            },
        });
    },
})
