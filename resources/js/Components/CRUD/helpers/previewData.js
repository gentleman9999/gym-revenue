import { ref } from "vue";

export const previewData = ref(null);

export const setPreviewData = (data) => (previewData.value = data);

export const clearPreviewData = () => (previewData.value = null);

export const preview = async (baseRoute, id) => {
    const routeName = `${baseRoute}.view`;
    const resp = await axios.get(route(routeName, id));
    setPreviewData(resp.data);
};
