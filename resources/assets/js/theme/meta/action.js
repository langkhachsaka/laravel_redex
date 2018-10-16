import ThemeConstant from './constant';

export function changeThemeTitle(title) {
    return {
        type: ThemeConstant.ACTION_CHANGE_TITLE,
        title: title
    }
}

export function changeThemeBreadcrumb(breadcrumb) {
    return {
        type: ThemeConstant.ACTION_CHANGE_BREADCRUMB,
        breadcrumb: breadcrumb
    }
}

export function openMainModal(body, title) {
    return {
        type: ThemeConstant.ACTION_OPEN_MAIN_MODAL,
        title: title,
        body: body,
    }
}

export function closeMainModal() {
    return {
        type: ThemeConstant.ACTION_CLOSE_MAIN_MODAL,
    }
}
