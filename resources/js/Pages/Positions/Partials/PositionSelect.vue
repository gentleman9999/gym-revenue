<template>
    <ApolloQuery :query="(gql) => queries['positions']">
        <template v-slot="{ result: { loading, error, data }, isLoading }">
            <template v-if="isLoading">
                <p>Loading...</p>
            </template>

            <template v-if="error">
                <jet-input-error :message="error" class="mt-2" />
            </template>

            <template v-if="data">
                <jet-label for="positions" value="Select Positions" />
                <select
                    v-model="localValue"
                    class="mt-1 w-full form-select"
                    id="positions"
                >
                    <option
                        v-for="{ label, value } in formatPosition(data)"
                        :value="value"
                        :key="value"
                    >
                        {{ label }}
                    </option>
                </select>
            </template>
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


const props = defineProps({
    modelValue: {
        type: String,
        default: "",
    },
});

const localValue = ref(props.modelValue);
const emit = defineEmits(["update:modelValue"]);

const formatPosition = (data) => {
  if (!data instanceof Array) return []
  return data.map((p) => {
    label: p.name,
    value: p.id
  })
}

watch(localValue, (nv, ov) => emit("update:modelValue", nv));
</script>
