import { ref } from "vue";

export const previewParam = ref(null);
export const editParam = ref(null);
export const createParam = ref(null);
export const crudName = ref(null);

export const clearPreviewParam = () => {
    previewParam.value = null;
};
export const clearEditParam = () => {
    editParam.value = null;
};
export const clearCreateParam = () => {
    createParam.value = null;
};

export const preview = async (id, name = "") => {
    crudName.value = name;
    previewParam.value = {
        id: id,
    };
};

export const edit = async (id, name = "") => {
    crudName.value = name;
    editParam.value = {
        id: id,
    };
};

export const create = async (id, name = "") => {
    crudName.value = name;
    createParam.value = {
        id: id,
    };
};
