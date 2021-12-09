import CrudBadge from "@/Components/CRUD/Fields/CrudBadge";

export const defaults = {
    boolean: CrudBadge
}

export const props = {
    'active': {
        getProps: ({data}) => !!data.active ? {text: 'Active', class: 'badge-success'} : {data: 'Draft', class: 'badge-warning'}
    }
}
