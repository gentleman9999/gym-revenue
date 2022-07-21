<template>
    <div class="form-control">
        <label for="client" class="label label-text">Team:</label>
        <select
            id="client"
            @input="$emit('update:modelValue', $event.target.value)"
            :disabled="loading"
            v-bind="$attrs"
        >
            <option :value="null">Select a team</option>
            <option v-for="team in teams" :value="team.id">
                {{ team.name }}
            </option>
        </select>
    </div>
</template>
<script setup>
import { ref, watch } from "vue";

const props = defineProps({
    clientId: {
        type: String,
        default: null,
    },
});

const emit = defineEmits("update:modelValue");

const teams = ref([]);
const loading = ref(false);

watch(
    () => props.clientId,
    async (clientId, oldClientId) => {
        if (clientId !== oldClientId) {
            loading.value = true;
            const response = await axios.get(
                route("clients.teams", { client_id: clientId })
            );
            teams.value = response.data;
            loading.value = false;
        }
    },
    { immediate: true }
);

// const emit = defineEmits('')

// onMounted(async ()=>{
//     const response = await axios.get(route('clients.teams'), props.client_id);
//     teams.value = response.data;
// })
</script>
