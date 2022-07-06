<template>
    <div v-if="loading" class="text-center">
        <label>Seeing Who You Can Impersonate...</label>
    </div>
    <div v-else>
        <p>
            Scroll through this list to select a user you want to impersonate.
        </p>
        <div v-if="users.length > 0" class="pt-4 max-h-60 overflow-y-scroll">
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

<script>
import { ref, defineEmits, onMounted } from "vue";
import { Inertia } from "@inertiajs/inertia";

const loading = ref(false);
const users = ref([]);
const emit = defineEmits(["close"]);
function getList() {
    loading.value = true;
    axios
        .post("/impersonation/users", {})
        .then(({ data }) => {
            loading.value = false;
            users.value = data;
        })
        .catch((err) => {
            loading.value = false;
            users.value = [];
        });
}
function impersonateUser(userId) {
    Inertia.post(route("impersonation.start", { victimId: userId }));
    emit("close");
}
onMounted(() => {
    getList();
});
</script>

<style scoped></style>
