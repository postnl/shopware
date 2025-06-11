
export default (() => {
    const context = import.meta.glob('./../decorator/**/!(*.spec).{j,t}s', {
        eager: true,
    });

    return Object.values(context).map((module) => module.default);
})();
