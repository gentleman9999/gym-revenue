import {ref} from "vue";

export const previewData = ref(null);

export const setPreviewData = (data) => previewData.value = data;

export const clearPreviewData = () => previewData.value = null;
