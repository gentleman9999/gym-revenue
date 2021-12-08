import BooleanBadge from "@/Components/CRUD/Fields/BooleanBadge";

export const defaults = {
    boolean: BooleanBadge
}

export const props = {
    'active': {
        truthy: 'Active',
        falsy: 'Inactive',
    }
}
