<template>
    <jet-form-section @submitted="handleSubmit" collapsable>
        <template #title>Social Media</template>

        <template #description> Fill out social medias</template>

        <template #form>
            <div
                class="col-span-6 sm:col-span-4 form-control flex-row items-center gap-4"
            >
                <jet-label for="facebook" value="Facebook" />
                <input id="facebook" type="text" v-model="form.facebook" />
            </div>
            <jet-input-error :message="form.errors.facebook" class="mt-2" />

            <div
                class="col-span-6 sm:col-span-4 form-control flex-row items-center gap-4"
            >
                <jet-label for="twitter" value="Twitter" />
                <input id="twitter" type="text" v-model="form.twitter" />
            </div>
            <jet-input-error :message="form.errors.twitter" class="mt-2" />

            <div
                class="col-span-6 sm:col-span-4 form-control flex-row items-center gap-4"
            >
                <jet-label for="instagram" value="Instagram" />
                <input id="instagram" type="text" v-model="form.instagram" />
            </div>
            <jet-input-error :message="form.errors.instagram" class="mt-2" />

            <div
                class="col-span-6 sm:col-span-4 form-control flex-row items-center gap-4"
            >
                <jet-label for="linkedin" value="Linkedin" />
                <input id="linkedin" type="text" v-model="form.linkedin" />
            </div>
            <jet-input-error :message="form.errors.linkedin" class="mt-2" />
        </template>

        <template #actions>
            <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </jet-action-message>

            <Button
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Save
            </Button>
        </template>
    </jet-form-section>
</template>

<script>
import { defineComponent } from "vue";
import JetActionMessage from "@/Jetstream/ActionMessage.vue";
import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";

import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import { useGymRevForm } from "@/utils";

export default defineComponent({
    components: {
        JetActionMessage,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
    },
    props: {
        availableSocialMedias: {
            type: Array,
            default: [],
        },
        socialMedias: {
            type: Array,
            default: [],
        },
    },
    setup(props) {
        let socialMedias = props.socialMedias;

        if (!socialMedias) {
            socialMedias = {
                facebook: "",
                twitter: "",
                instagram: "",
                linkedin: "",
            };
        } else {
            socialMedias.facebook = socialMedias.facebook;
            socialMedias.twitter = socialMedias.twitter;
            socialMedias.instagram = socialMedias.instagram;
            socialMedias.linkedin = socialMedias.linkedin;
        }

        const form = useGymRevForm(socialMedias);

        let handleSubmit = () =>
            form.post(route("settings.social-media.update"));

        return { form, handleSubmit };
    },
});
</script>
