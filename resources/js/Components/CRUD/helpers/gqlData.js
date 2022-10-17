import { ref } from "vue";

export const purpose = ref("preview");
export const queryParam = ref(null);

export const clearQueryParam = () => {
    queryParam.value = null;
};

export const preview = async (id) => {
    purpose.value = "preview";
    queryParam.value = {
        id: id,
    };
};

export const edit = async (id) => {
    purpose.value = "edit";
    queryParam.value = {
        id: id,
    };
};
