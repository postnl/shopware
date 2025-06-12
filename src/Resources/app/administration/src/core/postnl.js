class PostNLClass {
    Utils = import('./service/util.service').default
}

const PostNLInstance = new PostNLClass()

export default { PostNLClass, PostNLInstance }