export function __hardcoded ($value) {
    return $value;
}

export function __deepClone ($object) {
    return JSON.parse(JSON.stringify($object));
}

export function toFormData (payload) {
    const formData = new FormData();
    Object.keys(payload).forEach(key => {
        const value = payload[key];
        if (Array.isArray(value)) {
            value.forEach(v => {
                formData.append(`${key}[]`, v);
            })
        } else {
            formData.append(key, value);
        }
    })
    return formData;
}

export function createObjectURL (file) {
    return window.URL.createObjectURL(file);
}
