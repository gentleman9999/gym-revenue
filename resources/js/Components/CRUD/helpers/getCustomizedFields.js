import {computed} from "vue";
import {getCrudConfig} from "@/utils/getCrudCustomization";

export const getCustomizedFields = (fields, modelKey) => {
    //now filter out fields we don't care about
    const config = getCrudConfig(modelKey);
    const filtered = computed(() => config.value?.length ? fields.value.filter(field => config.value.find(f => f === field.name)) : fields.value);
    return filtered;
};
