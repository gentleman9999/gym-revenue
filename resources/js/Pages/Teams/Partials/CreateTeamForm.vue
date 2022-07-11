<template>
    <jet-form-section @submitted="createTeam">
        <template #title> Team Details </template>

        <template #description>
            Create a new team to collaborate with others on projects.
        </template>

        <template #form>
            <div class="col-span-6">
                <jet-label value="Team Owner" />

                <div class="flex items-center mt-2">
                    <img
                        class="object-cover w-12 h-12 rounded-full"
                        :src="$page.props.user.profile_photo_url"
                        :alt="$page.props.user.name"
                    />

                    <div class="ml-4 leading-tight">
                        <div>{{ $page.props.user.name }}</div>
                        <div class="text-sm">{{ $page.props.user.email }}</div>
                    </div>
                </div>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <jet-label for="name" value="Team Name" />
                <input
                    id="name"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.name"
                    autofocus
                />
                <jet-input-error :message="form.errors.name" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <Button
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing || !form.isDirty"
            >
                Create
            </Button>
        </template>
    </jet-form-section>
</template>

<script setup>
import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";

import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import { useGymRevForm } from "@/utils";
import { usePage } from "@inertiajs/inertia-vue3";

const page = usePage();
const form = useGymRevForm({
    name: "",
    user_id: page.props.user.id,
    personal_team: false,
});
function createTeam() {
    form.post(route("teams.store"), {
        errorBag: "createTeam",
        preserveScroll: true,
    });
}
</script>
