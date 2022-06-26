<template>
    <jet-form-section @submitted="handleSubmit">
        <template #title> Team Details</template>

        <template #description>
            Create a new team to collaborate with others on projects.
        </template>

        <template #form>
            <div class="col-span-6">
                <jet-label value="Team Owner" />

                <div class="flex items-center mt-2">
                    <img
                        class="object-cover w-12 h-12 rounded-full"
                        :src="
                            operation === 'Create'
                                ? $page.props.user.profile_photo_url
                                : team.owner.profile_photo_url
                        "
                        :alt="
                            operation === 'Create'
                                ? $page.props.user.name
                                : team.owner.name
                        "
                    />

                    <div class="ml-4 leading-tight">
                        <div>
                            {{
                                operation === "Create"
                                    ? $page.props.user.name
                                    : team.owner.name
                            }}
                        </div>
                        <div class="text-sm">
                            {{
                                operation === "Create"
                                    ? $page.props.user.email
                                    : team.owner.email
                            }}
                        </div>
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
                :disabled="form.processing"
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
import Button from "@/Components/Button";
import JetFormSection from "@/Jetstream/FormSection";
import JetActionMessage from "@/Jetstream/ActionMessage";
import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";
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
    },

    setup(props) {
        const page = usePage();
        let operation = "Update";
        let team = props.team;
        if (!team) {
            team = {
                client_id: page.props.value.user?.current_client_id,
                name: "",
                user_id: page.props.value.user.id,
                personal_team: false,
                locations: [],
            };
            operation = "Create";
        } else {
            team.locations = page.props.value.locations.map(
                (detail) => detail.value
            );
            team.client_id = page.props.value.user?.current_client_id;
            console.log("team.locations", team.locations);
        }
        const form = useGymRevForm(team);

        let handleSubmit = () =>
            form.dirty().put(route("team.update", team.id));
        if (operation === "Create") {
            handleSubmit = () => form.dirty().post(route("teams.store"));
        }

        return {
            form,
            operation,
            handleSubmit,
            page,
            availableLocations: page.props.value.availableLocations,
            multiselectClasses: getDefaultMultiselectTWClasses(),
        };
    },
});
</script>
