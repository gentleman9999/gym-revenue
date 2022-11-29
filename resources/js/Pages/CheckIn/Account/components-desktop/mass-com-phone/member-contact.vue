<template>
    <div class="mass-com-phone-container">
        <img class="phone-avatar" :src="avatar" />
        <div class="phone-name">
            {{ data?.user.first_name }} {{ data?.user.last_name }}
        </div>
        <update-member @toggle-mode="$emit('toggle-mode', 'list')" />
        <div class="flex flex-col space-y-6">
            <input-with-label label="Phone" :value="form.phone" secondary />
            <input-with-label label="Email" :value="form.email" secondary />
        </div>
        <div class="mass-com-action">
            Contact
            <Button
                secondary
                outline
                size="xs"
                class="ml-8"
                @click="$emit('toggle-mode', 'note')"
                >+ Email</Button
            >
        </div>
    </div>
</template>
<style scoped>
.mass-com-phone-container {
    @apply flex flex-col items-center;
}
.phone-avatar {
    @apply h-14 w-14 rounded-full mb-2;
}
.phone-name {
    @apply text-xl font-bold;
}
.mass-com-action {
    @apply self-start mt-8;
}
</style>
<script setup>
import { computed } from "vue";
import { getAvatarImg, useGymRevForm } from "@/utils";
import Button from "@/Components/Button.vue";
import UpdateMember from "./update-member.vue";
import InputWithLabel from "@/Pages/CheckIn/components/input-with-label.vue";
const props = defineProps({
    data: {
        type: Object,
        default: {
            user: {
                first_name: "Tommy",
                last_name: "Lee",
            },
        },
    },
});

const avatar = computed(() =>
    getAvatarImg(
        props.data?.user.first_name || "Tommy",
        props.data?.user.last_name || "Lee"
    )
);

const form = useGymRevForm({});
</script>
