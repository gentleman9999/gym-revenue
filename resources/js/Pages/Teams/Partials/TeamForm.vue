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

            <div class="col-span-6 sm:col-span-4" v-if="availableLocations?.length">
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
import { useForm, usePage } from "@inertiajs/inertia-vue3";
import Button from "@/Components/Button";
import JetFormSection from "@/Jetstream/FormSection";
import JetActionMessage from "@/Jetstream/ActionMessage";
import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";
import Multiselect from "@vueform/multiselect";
import {getDefaultMultiselectTWClasses} from "@/utils";

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
                name: "",
                user_id: page.props.value.user.id,
                personal_team: false,
                locations: [],
            };
            operation = "Create";
        } else {
            team.locations = page.props.value.locations.map(detail=>detail.value);
            console.log('team.locations', team.locations);
        }
        const form = useForm(team);

        let handleSubmit = () => form.put(route("teams.update", team.id));
        if (operation === "Create") {
            handleSubmit = () => form.post(route("teams.store"));
        }

        const multiselectClasses = {
            container:
                "relative mx-auto w-full flex items-center justify-end box-border cursor-pointer border border-2 border-base-content border-opacity-20 rounded-lg bg-base-100 text-base leading-snug outline-none min-h-12",
            containerDisabled: "cursor-default bg-base-200",
            containerOpen: "rounded-b-none",
            containerOpenTop: "rounded-t-none",
            containerActive: "ring ring-primary",
            singleLabel:
                "flex items-center h-full max-w-full absolute left-0 top-0 pointer-events-none bg-transparent leading-snug pl-3.5 pr-16 box-border",
            singleLabelText:
                "overflow-ellipsis overflow-hidden block whitespace-nowrap max-w-full",
            multipleLabel:
                "flex items-center h-full absolute left-0 top-0 pointer-events-none bg-transparent leading-snug pl-3.5",
            search: "w-full absolute inset-0 outline-none focus:ring-0 appearance-none box-border border-0 text-base font-sans bg-base-100 rounded pl-3.5",
            tags: "flex-grow flex-shrink flex flex-wrap items-center mt-1 pl-2",
            tag: "bg-primary text-base-content text-sm font-semibold py-0.5 pl-2 rounded mr-1 mb-1 flex items-center whitespace-nowrap",
            tagDisabled: "pr-2 opacity-50",
            tagRemove:
                "flex items-center justify-center p-1 mx-0.5 rounded-sm hover:bg-black hover:bg-opacity-10 group",
            tagRemoveIcon:
                "bg-multiselect-remove text-base-con bg-center bg-no-repeat opacity-30 inline-block w-3 h-3 group-hover:opacity-60",
            tagsSearchWrapper:
                "inline-block relative mx-1 mb-1 flex-grow flex-shrink h-full",
            tagsSearch:
                "absolute inset-0 border-0 outline-none focus:ring-0 appearance-none p-0 text-base font-sans box-border w-full",
            tagsSearchCopy: "invisible whitespace-pre-wrap inline-block h-px",
            placeholder:
                "flex items-center h-full absolute left-0 top-0 pointer-events-none bg-transparent leading-snug pl-3.5 text-base-content text-opacity-50",
            caret: "bg-multiselect-caret bg-center bg-no-repeat w-2.5 h-4 py-px box-content mr-3.5 relative z-10 opacity-40 flex-shrink-0 flex-grow-0 transition-transform transform pointer-events-none",
            caretOpen: "rotate-180 pointer-events-auto",
            clear: "pr-3.5 relative z-10 opacity-40 transition duration-300 flex-shrink-0 flex-grow-0 flex hover:opacity-80",
            clearIcon:
                "bg-multiselect-remove bg-center bg-no-repeat w-2.5 h-4 py-px box-content inline-block",
            spinner:
                "bg-multiselect-spinner bg-center bg-no-repeat w-4 h-4 z-10 mr-3.5 animate-spin flex-shrink-0 flex-grow-0",
            dropdown:
                "max-h-60 absolute -left-px -right-px bottom-0 transform translate-y-full border border-gray-300 -mt-px overflow-y-scroll z-50 bg-base-100 flex flex-col rounded-b",
            dropdownTop:
                "-translate-y-full top-px bottom-auto flex-col-reverse rounded-b-none rounded-t",
            dropdownHidden: "hidden",
            options: "flex flex-col p-0 m-0 list-none",
            optionsTop: "flex-col-reverse",
            group: "p-0 m-0",
            groupLabel:
                "flex text-sm box-border items-center justify-start text-left py-1 px-3 font-semibold bg-base-300 cursor-default leading-normal",
            groupLabelPointable: "cursor-pointer",
            groupLabelPointed: "bg-base-300 text-base-content text-opacity-70",
            groupLabelSelected: "bg-green-600 text-base-content",
            groupLabelDisabled:
                "bg-base-200 text-base-content text-opacity-50 cursor-not-allowed",
            groupLabelSelectedPointed:
                "bg-green-600 text-base-content opacity-90",
            groupLabelSelectedDisabled:
                "text-green-100 bg-green-600 bg-opacity-50 cursor-not-allowed",
            groupOptions: "p-0 m-0",
            option: "flex items-center justify-start box-border text-left cursor-pointer text-base leading-snug py-2 px-3",
            optionPointed: "bg-primary",
            optionSelected: "text-base-content bg-green-500",
            optionDisabled:
                "text-base-content text-opacity-50 cursor-not-allowed",
            optionSelectedPointed: "text-base-content bg-green-500 opacity-90",
            optionSelectedDisabled:
                "text-green-100 bg-green-500 bg-opacity-50 cursor-not-allowed",
            noOptions:
                "py-2 px-3 text-base-content text-opacity-50 bg-base-100 text-left",
            noResults:
                "py-2 px-3 text-base-content text-opacity-50 bg-base-100 text-left",
            fakeInput:
                "bg-transparent absolute left-0 right-0 -bottom-px w-full h-px border-0 p-0 appearance-none outline-none text-transparent",
            spacer: "h-9 py-px box-content",
        };
        console.log(";availablelocations", page.props.value);
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
