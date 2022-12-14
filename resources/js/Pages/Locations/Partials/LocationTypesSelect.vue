<template>
    <ApolloQuery :query="(gql) => queries['locationTypes']">
        <template v-slot="{ result: { loading, error, data }, isLoading }">
            <!-- this seems to work, while 'loading' does not -->
            <template v-if="isLoading">
                <p>Loading...</p>
            </template>

            <template v-if="error">
                <jet-input-error :message="error" class="mt-2" />
            </template>

            <template v-if="data">
                <jet-label for="location_type" value="Location Types" />
                <Multiselect
                    v-bind="$attrs"
                    id="location_type"
                    class="mt-1 multiselect"
                    v-model="localValue"
                    :searchable="true"
                    :options="formatLocationTypeForSelect(data.locationTypes)"
                    :classes="getDefaultMultiselectTWClasses()"
                />
            </template>

            <!-- <div v-else>No result</div> -->
        </template>
    </ApolloQuery>
</template>

<script setup>
import { ref, defineEmits, watch } from "vue";
import queries from "@/gql/queries";
import { formatLocationTypeForSelect } from "@/utils/locationTypeEnum";
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

const localValue = ref(props.modelValue);
const emit = defineEmits(["update:modelValue"]);

watch(localValue, (nv, ov) => emit("update:modelValue", nv));
</script>
