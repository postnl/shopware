const context = import.meta.glob('./../decorator/**/!(*.spec).{j,t}s', { eager: true });

Object.values(context).map((module) => module.default);