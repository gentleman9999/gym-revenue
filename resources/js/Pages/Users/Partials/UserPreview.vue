<template>
    <div class="grid grid-cols-6 gap-4">
        <div class="field col-span-6 lg:col-span-3">
            <label>Name:</label>
            <div class="data">
                {{ user.name }}
            </div>
        </div>
        <div class="field col-span-6 lg:col-span-3">
            <label>Email:</label>
            <div class="data">
                {{ user.email }}
            </div>
        </div>

        <div class="field col-span-6 lg:col-span-3">
            <label>Phone:</label>
            <div class="data">
                {{ user.phone }}
            </div>
        </div>

        <div class="field col-span-6 lg:col-span-3">
            <label>Security Role:</label>
            <div class="data">
                {{ user.role }}
            </div>
        </div>
        <div class="field col-span-6 lg:col-span-3" v-if="user.classification.value">
            <label>Classification:</label>
            <div class="data">
                {{ user.classification.value }}
            </div>
        </div>
        <template v-if="user.teams?.length">
            <label class="col-span-6">Teams:</label>
            <a
                v-for="team in user.teams"
                class="col-span-6 xl:col-span-3 bg-primary bg-opacity-25 py-2 px-4 rounded-lg flex flex-row"
                :href="route('teams', { preview: team.id })"
            >
                {{ team.name }}
                <div class="flex-grow" />
            </a>
        </template>
        <template v-if="user.files?.length">
            <label class="col-span-6">Files:</label>
            <a
                v-for="file in user.files"
                class="col-span-6 xl:col-span-3 bg-primary bg-opacity-25 py-2 px-4 rounded-lg flex flex-row"
                :href="file.url"
                :download="file.filename"
                target="_blank"
            >
                {{ file.filename }}
                <div class="flex-grow" />
            </a>
        </template>
    </div>
</template>

<style scoped>
input {
    @apply input-xs;
}
.field {
    @apply flex flex-row gap-2;
}
</style>

<script>
export default {
    props: ["user"],
};
</script>
