<template>
    <ApolloQuery :query="(gql) => queries['locations']">
        <template v-slot="{ result: { loading, error, data }, isLoading }">
            <template v-if="isLoading">
                <p>Loading...</p>
            </template>

            <template v-if="error">
                <jet-input-error :message="error" class="mt-2" />
            </template>

            <template v-if="data">
                <jet-label for="club_id" value="Club" />
                <select
                    v-bind="$attrs"
                    class="w-full"
                    id="club_id"
                    :required="required"
                    v-model="localValue"
                >
                    <option value="">Select a Club</option>
                    <option
                        v-for="location in data.locations.data"
                        :key="location.id"
                        :value="location.id"
                    >
                        {{ location.name }}
                    </option>
                </select>
            </template>
        </template>
    </ApolloQuery>
</template>

<script setup>
import { ref, defineEmits, watch } from "vue";
import queries from "@/gql/queries";
import JetLabel from "@/Jetstream/Label.vue";
import JetInputError from "@/Jetstream/InputError.vue";

const props = defineProps({
    modelValue: {
        type: String,
        default: "",
    },
    required: {
        type: Boolean,
        default: false,
    },
});

const localValue = ref(props.modelValue);
const emit = defineEmits(["update:modelValue"]);

watch(localValue, (nv, ov) => emit("update:modelValue", nv));
</script>
