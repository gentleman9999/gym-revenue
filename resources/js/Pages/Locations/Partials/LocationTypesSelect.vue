<template>
    <ApolloQuery :query="(gql) => queries['locationTypes']">
        <template v-slot="{ result: { loading, error, data } }">
            <div v-if="loading" class="loading apollo">Loading...</div>

            <template v-else-if="error">
                <jet-input-error :message="error" class="mt-2" />
            </template>

            <template v-else-if="data">
                <jet-label for="location_type" value="Location Types" />
                <Multiselect
                    v-bind="$attrs"
                    id="location_type"
                    class="mt-1 multiselect"
                    v-model="localValue"
                    :searchable="true"
                    :options="fmt(data.locationTypes)"
                    :classes="getDefaultMultiselectTWClasses()"
                />
            </template>

            <div v-else>No result</div>
        </template>
    </ApolloQuery>
</template>

<script setup>
import { ref, defineEmits, watch } from "vue";
import queries from "@/gql/queries";
import { parseLocationTypeDisplayName } from "@/utils/locationTypeEnum";
import { getDefaultMultiselectTWClasses } from "@/utils";
import JetLabel from "@/Jetstream/Label.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import Multiselect from "@vueform/multiselect";

const props = defineProps({
    modelValue: {
        type: String,
        default: "",
    },
});

// local state
const localValue = ref(props.modelValue);

/**
 * format location type return data for multiselect component
 * @param {Array} data location types
 */
function fmt(data) {
    if (data instanceof Array) {
        console.log("is instance of array. data.", data);
    }
    let arr = [];
    data.forEach((lt) => {
        arr.push({
            value: lt.value?.toUpperCase(),
            label: parseLocationTypeDisplayName(lt),
        });
    });
    return arr;
}

const emit = defineEmits(["update:modelValue"]);

watch(localValue, (nv, ov) => emit("update:modelValue", nv));
</script>
