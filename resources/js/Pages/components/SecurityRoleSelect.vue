<template>
    <div v-if="rolesLoading">
        <span class="block py-4">Loading...</span>
    </div>
    <div v-if="!rolesLoading && !!resources">
        <jet-label for="role_id" value="Security Role" />
        <select
            v-model="localValue"
            v-bind="$attrs"
            class="w-full"
            id="role_id"
            :required="required"
        >
            <option disabled value="" selected>Select a Security Role</option>
            <option
                v-for="role in resources.data"
                :key="role.id"
                :value="role.id"
                :selected="role.id === localValue"
            >
                {{ role.title }}
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

const param = ref({
    page: 1,
});

const mountingFinished = ref(false);
const rolesLoading = ref(true);

const { result, loading, error, refetch } = useQuery(
    queries["roles"],
    props.param ? props.param : param,
    { throttle: 500 }
);

const resources = computed(() => {
    if (result.value && result.value["roles"]) {
        return _.cloneDeep(result.value["roles"]);
    } else return null;
});

watch(loading, (nv, ov) => {
    if (!mountingFinished.value) return;
    if (!!resources?.value) {
        rolesLoading.value = false;
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
    rolesLoading.value = false;
});
</script>
