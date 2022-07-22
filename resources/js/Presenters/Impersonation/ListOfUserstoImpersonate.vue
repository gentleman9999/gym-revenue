<template>
    <div :hidden="!loading" class="text-center">
        <label>Seeing Who You Can Impersonate...</label>
    </div>
    <div :hidden="loading">
        <h1>Impersonation</h1>
        <clients-dropdown
            v-model="selected.client"
            v-if="page.props.value.user.is_gr_admin"
        />
        <client-teams-dropdown
            v-model="selected.team"
            :client-id="selected.client"
            v-if="
                page.props.value.user.is_gr_admin ||
                page.props.value.user.all_teams?.length
            "
        />
        <div v-if="users?.length > 0" class="pt-4 max-h-60 overflow-y-scroll">
            <table
                class="table w-full table-compact"
                :class="{ 'table-zebra': true }"
            >
                <tbody>
                    <tr v-for="(user, idx) in users">
                        <td>{{ user.name }}</td>
                        <td>{{ user.role }}</td>
                        <td>
                            <button
                                type="button"
                                class="btn btn-info"
                                @click="impersonateUser(user.userId)"
                            >
                                Impersonate
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-else class="text-center">
            <p>No Users Available that you can Impersonate.</p>
        </div>
    </div>
</template>

<script setup>
import { ref, defineEmits, watch, computed } from "vue";
import { Inertia } from "@inertiajs/inertia";
import ClientsDropdown from "@/Presenters/Impersonation/Partials/ClientsDropdown.vue";
import ClientTeamsDropdown from "@/Presenters/Impersonation/Partials/ClientTeamsDropdown.vue";
import { usePage } from "@inertiajs/inertia-vue3";

const loading = ref(false);
const response = ref(null);

const users = ref([]);

const page = usePage();

const selected = ref({
    client: null,
    team: page.props.value.user.current_team_id,
});

const emit = defineEmits(["close"]);
function getList() {
    if (selected.value.team) {
        loading.value = true;
        axios
            .post("/impersonation/users", { team: selected.value.team })
            .then(({ data }) => {
                loading.value = false;
                response.value = data;
                users.value = data;
            })
            .catch((err) => {
                loading.value = false;
                users.value = [];
            });
    }
}
function impersonateUser(userId) {
    Inertia.post(route("impersonation.start", { victimId: userId }));
    emit("close");
}
watch(() => selected.value.team, getList, { immediate: true });
watch(
    () => selected.value.client,
    (clientId, oldClientId) => {
        if (clientId !== oldClientId) {
            selected.value.team = null;
            users.value = [];
        }
    },
    { immediate: true }
);
</script>

<style scoped></style>
