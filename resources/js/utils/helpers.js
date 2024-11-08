import { cloneDeep } from 'lodash';

export function __hardcoded($value) {
    return $value;
}

export function __deepClone($object) {
    return cloneDeep($object);
}

export function __debounce(cb, delay = 500) {
    let timeoutID = null;
    return function () {
        clearTimeout(timeoutID);
        const args = arguments;
        const that = this;
        timeoutID = setTimeout(function () {
            cb.apply(that, args);
        }, delay);
    };
}

export function toFormData(payload) {
    const formData = new FormData();
    Object.keys(payload).forEach((key) => {
        const value = payload[key];
        if (Array.isArray(value)) {
            value.forEach((v) => {
                formData.append(`${key}[]`, v);
            });
        } else {
            formData.append(key, value);
        }
    });
    return formData;
}

export function createObjectURL(file) {
    return window.URL.createObjectURL(file);
}

export function fileDownload(path) {
    const link = document.createElement('a');
    link.href = `${window.location.origin}/${path}`;
    link.click();
    link.download = true;
    link.remove();
}

export function getNumbersInRange(from, to) {
    if (from > to) {
        return []; // Возвращаем пустой массив, если from > to
    }

    const result = [];
    for (let i = from; i <= to; i++) {
        result.push(i);
    }

    return result;
}

export function _getFileType(filename) {
    const extension = filename.split('.').pop().toLowerCase();

    const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];

    const videoExtensions = ['mp4', 'avi', 'mkv', 'mov', 'webm'];

    if (imageExtensions.includes(extension)) {
        return 'image';
    } else if (videoExtensions.includes(extension)) {
        return 'video';
    } else {
        return undefined;
    }
}

export function _generateImagePreview(file) {
    if (!file) {
        return null;
    }
    return createObjectURL(file);
}

export function isObject(variable) {
    return (
        typeof variable === 'object' &&
        variable !== null &&
        !Array.isArray(variable) &&
        !(variable instanceof Function)
    );
}

export function _extractError(error) {
    if (!isObject(error) || !isObject(error.response)) {
        return 'Неизвестная ошибка';
    }

    if (error.response.status === 500) {
        console.error(error.response);
        return 'На сервере произошла ошибка';
    }

    if (error.response.data && error.response.data.message) {
        // Проверяем, является ли message массивом
        if (Array.isArray(error.response.data.message)) {
            // Возвращаем все сообщения, объединенные в одну строку
            return error.response.data.message.join(', ');
        }
        // Возвращаем сообщение, если оно не массив
        return error.response.data.message;
    }

    if (error.response.message) {
        return error.response.message;
    }

    // Проверка на наличие других возможных полей, где может быть сообщение
    if (error.response.error) {
        return error.response.error;
    }

    if (error.response.statusText) {
        return error.response.statusText;
    }

    return 'Неизвестная ошибка';
}
