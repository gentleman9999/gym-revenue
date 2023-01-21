<template>
    <div v-if="audiencesLoading">
        <span class="text-black block py-4">Loading...</span>
    </div>

    <template v-if="error">
        <jet-input-error :message="error" class="mt-2" />
    </template>

    <div v-if="!audiencesLoading && !!resources">
        <slot name="label" />
        <select
            v-model="localValue"
            v-bind="$attrs"
            class="bg-base-content text-black p-2 rounded-none border border-black w-full mb-4"
            id="audience"
            :required="required"
        >
            <option disabled="true" value="" selected>Select Audience</option>
            <option
                v-for="audience in resources.data"
                :disabled="audience.title === 'Select an Audience'"
                :key="audience.id"
                :value="audience.id"
                :selected="audience.id === localValue"
            >
                {{ audience.name }}
            </option>
        </select>
    </div>
</template>

<script setup>
import { ref, defineEmits, watch, computed, onMounted } from "vue";
import queries from "@/gql/queries";
import JetLabel from "@/Jetstream/Label.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import { useQuery } from "@vue/apollo-composable";

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

const mountingFinished = ref(false);
const audiencesLoading = ref(true);

const param = ref({
    page: 1,
});

const { result, loading, error, refetch } = useQuery(
    queries["audiences"],
    props.param ? props.param : param,
    { throttle: 500 }
);

const resources = computed(() => {
    if (result.value && result.value["audiences"]) {
        return _.cloneDeep(result.value["audiences"]);
    } else return null;
});

watch(loading, (nv, ov) => {
    if (!mountingFinished.value) return;
    if (!!resources?.value) {
        audiencesLoading.value = false;
    }
});

const localValue = ref(props.modelValue);
const emit = defineEmits(["update:modelValue"]);

watch(localValue, async (nv, ov) => {
    await refetch();
    emit("update:modelValue", nv);
});

onMounted(async () => {
    await refetch();
    mountingFinished.value = true;
    audiencesLoading.value = false;
});
</script>
