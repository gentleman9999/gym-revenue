import prettyBytes from "pretty-bytes";

export const transforms = {
    datetime: (val) => new Date(val).toLocaleString(),
    bytes: (val) => prettyBytes(val),
};

export const defaults = {
    created_at: transforms.datetime,
    updated_at: transforms.datetime,
    deleted_at: transforms.datetime,
    size: transforms.bytes,
}
