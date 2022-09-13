export function __hardcoded ($value) {
    return $value;
}

export function __deepClone ($object) {
    return JSON.parse(JSON.stringify($object));
}
