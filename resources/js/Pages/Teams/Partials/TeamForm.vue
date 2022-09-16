<template>
    <jet-form-section @submitted="handleSubmit">
        <template #title> Team Details</template>

        <template #description>
            Create a new team to collaborate with others on projects.
        </template>

        <template #form>
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

            <div
                class="col-span-6 sm:col-span-4"
                v-if="availableLocations?.length"
            >
                <jet-label for="locations" value="Locations" />
                <multiselect
                    v-model="form.locations"
                    id="locations"
                    mode="tags"
                    :close-on-select="false"
                    :create-option="true"
                    :options="
                        availableLocations.map((location) => ({
                            label: location.name,
                            value: location.gymrevenue_id,
                        }))
                    "
                    :classes="multiselectClasses"
                />
                <jet-input-error
                    :message="form.errors.locations"
                    class="mt-2"
                />
            </div>
        </template>

        <template #actions>
            <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </jet-action-message>
            <Button
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing || !form.isDirty"
                primary
            >
                Save
            </Button>
        </template>
    </jet-form-section>
</template>

<script>
import { defineComponent } from "vue";
import { usePage } from "@inertiajs/inertia-vue3";
import { useGymRevForm } from "@/utils";
import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetActionMessage from "@/Jetstream/ActionMessage.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import Multiselect from "@vueform/multiselect";
import { getDefaultMultiselectTWClasses } from "@/utils";

export default defineComponent({
    components: {
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
        JetActionMessage,
        Multiselect,
    },
    props: {
        team: {
            type: Object,
        },
        availableLocations: {
            type: Array,
            required: true,
        },
        locations: {
            type: Array,
            required: false,
            default: [],
        },
    },

    setup(props) {
        const page = usePage();
        let operation = "Update";
        let team = props.team;
        if (!team) {
            team = {
                name: "",
                locations: [],
            };
            operation = "Create";
        } else {
            team.locations = props.locations.map((detail) => detail.value);
            team.client_id = page.props.value.user?.client_id;
            console.log("team.locations", team.locations);
        }
        const form = useGymRevForm(team);

        let handleSubmit = () =>
            form.dirty().put(route("team.update", team.id));
        if (operation === "Create") {
            handleSubmit = () => form.post(route("teams.store"));
        }

        return {
            form,
            operation,
            handleSubmit,
            page,
            multiselectClasses: getDefaultMultiselectTWClasses(),
        };
    },
});
</script>
