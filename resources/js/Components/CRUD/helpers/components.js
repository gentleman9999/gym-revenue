import BooleanBadge from "@/Components/CRUD/Fields/BooleanBadge";

export const defaults = {
    boolean: BooleanBadge
}

export const props = {
    'active': {
        getProps: ({data}) => !!data.active ? {text: 'Active', class: 'badge-success'} : {data: 'Draft', class: 'badge-warning'}
    }
}
