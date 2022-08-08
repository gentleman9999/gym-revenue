<template>
    <div class="grid grid-cols-12 gap-3">
        <div class="preview-title">User Preview</div>
        <div class="preview-avatar">
            <img :src="avatar" />
        </div>
        <div class="field">
            <div>First Name</div>
            <div class="data">{{ user.first_name }}</div>
        </div>
        <div class="field">
            <div>Middle Name</div>
            <div class="data">
                {{ user.middle_name ? user.middle_name : "" }}
            </div>
        </div>
        <div class="field">
            <div>Last Name</div>
            <div class="data">{{ user.last_name }}</div>
        </div>
        <div class="field">
            <div>Email</div>
            <div class="data">{{ user.email }}</div>
        </div>
        <div class="field">
            <div>Primary Phone</div>
            <div class="data">{{ user.phone }}</div>
        </div>
        <div class="field">
            <div>Security Role</div>
            <div class="data">{{ user.role }}</div>
        </div>

        <div class="field">
            <div>City</div>
            <div class="data">{{ user.city }}</div>
        </div>
        <div class="field">
            <div>Zip</div>
            <div class="data">{{ user.zip }}</div>
        </div>
        <div class="field">
            <div>Manager</div>
            <div class="data">{{ user.manager }}</div>
        </div>
        <div class="col-span-12">
            <label>Teams:</label>
            <div class="flex flex-row flex-wrap">
                <div
                    v-for="team in user.teams"
                    :key="team.id"
                    class="md:w-1/6 p-1 w-full"
                >
                    <label class="flex data">
                        {{ team.name }}
                    </label>
                </div>
            </div>
        </div>
        <!-- 
        <div>{{user.teams[0].name}}</div> -->

        <template v-if="user.files?.length">
            <label class="col-span-6">Files:</label>
            <a
                v-for="(file, ndx) in user.files"
                :key="ndx"
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
.preview-title {
    @apply col-span-12 mt-[-20px] text-secondary text-base;
}
.preview-avatar {
    @apply col-span-12;
}
.preview-avatar img {
    @apply rounded-full m-auto;
}
input {
    @apply input-xs;
}
.field {
    @apply flex flex-col gap-2 col-span-12 lg:col-span-4;
}
.data {
    @apply bg-secondary rounded px-3 py-1 h-8;
}
</style>

<script setup>
import { computed } from "vue";
import { getAvatarImg } from "@/utils";
const props = defineProps({
    user: {
        type: Object,
    },
});

const avatar = computed(() =>
    getAvatarImg(props.user.first_name, props.user.last_name)
);
</script>
