import utils from './service/util.service'

class PostNLClass {
    Utils = utils
}

const PostNLInstance = new PostNLClass()

export default { PostNLClass, PostNLInstance }