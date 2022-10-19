import { ref } from "vue";

export const previewParam = ref(null);
export const editParam = ref(null);

export const clearPreviewParam = () => {
    previewParam.value = null;
};
export const clearEditParam = () => {
    editParam.value = null;
};

export const preview = async (id) => {
    previewParam.value = {
        id: id,
    };
};

export const edit = async (id) => {
    editParam.value = {
        id: id,
    };
};
