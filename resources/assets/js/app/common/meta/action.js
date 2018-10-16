import Constant from './constant';

export function search(params) {
    return {
        type: Constant.ACTION_SEARCH,
        params: params
    }
}

export function changePage(page) {
    return {
        type: Constant.ACTION_CHANGE_PAGE,
        page: page
    }
}

export function changePageSize(pageSize) {
    return {
        type: Constant.ACTION_CHANGE_PAGE_SIZE,
        pageSize: pageSize
    }
}

export function clearState() {
    return {
        type: Constant.ACTION_CLEAR_STATE
    }
}
