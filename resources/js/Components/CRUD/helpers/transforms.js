import prettyBytes from "pretty-bytes";

export const transforms = {
    noop: (val) => val,
    datetime: (val) => new Date(val).toLocaleString(),
    bytes: (val) => prettyBytes(val),
    boolean: (val) => Boolean(val),
};

export const defaults = {
    created_at: transforms.datetime,
    updated_at: transforms.datetime,
    deleted_at: transforms.datetime,
    size: transforms.bytes,
    active: transforms.boolean,
};
