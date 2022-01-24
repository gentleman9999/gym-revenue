<template>
    <app-layout :title="title">
        <template #header>
            <h2 class="font-semibold text-xl leading-tight">Locations </h2>
<!--           {{this.$page.props}}-->
        </template>
        <gym-revenue-crud
            base-route="locations"
            model-name="Location"
            :fields="fields"
            :resource="locations"
            :actions="{
               trash:{
                   label: 'Close Club',
                    handler: ({data}) => handleClickTrash(data.id)
                },
            }"
        ><!--base-route="locations"-->
            <template #filter>
                <search-filter
                    v-model:modelValue="form.search"
                    class="w-full max-w-md mr-4"
                    @reset="reset"
                >
                    <template #content>
                        <div class="block px-4 py-2 text-xs w-60">
            <div class="block py-2 text-xs text-white">Closed Clubs:</div>
        <select
            v-model="form.trashed"
            class="mt-1 w-full form-select"
        >
            <option :value="null" />
            <option value="with">With Closed</option>
            <option value="only">Only Closed</option>
        </select>
        <div  class="block py-2 text-xs text-white">State:</div>
        <select
            v-model="form.state"
            class="mt-1 w-full form-select"
        >
            <option :value="null" />
            <option v-for="(state, i) in this.$page.props.eachstate"  :value="state.state">
                {{state.state}}            </option>

        </select>
                        </div>
                    </template>
                </search-filter>
                </template>
        </gym-revenue-crud>
        <confirm
            title="Really Close This Club?"
            v-if="confirmTrash"
            @confirm="handleConfirmTrash"
            @cancel="confirmTrash = null"
        >
            Are you sure you want to Close this Club?<BR/>
        </confirm>
    </app-layout>
</template>
<script>
import { defineComponent,ref  } from "vue";
import AppLayout from "@/Layouts/AppLayout";
import GymRevenueCrud from "@/Components/CRUD/GymRevenueCrud";
//import {ref} from "vue/dist/vue";
import SearchFilter from "@/Components/CRUD/SearchFilter";
import {Inertia} from "@inertiajs/inertia";
import Confirm from "@/Components/Confirm";

import Button from "@/Components/Button";
import JetBarContainer from "@/Components/JetBarContainer";
import {useSearchFilter} from "@/Components/CRUD/helpers/useSearchFilter";

export default defineComponent({
    components: {
        AppLayout,
        GymRevenueCrud,
        Confirm,
        JetBarContainer,
        Button,
        SearchFilter,

    },
    props: ["sessions", "locations", "title", "isClientUser", "filters","useSearchFilter","SearchFilter"],
    setup(props) {

        const baseRoute = "locations";
        const { form, reset } = useSearchFilter(baseRoute, {
              //  preserveState: false,
            },
        );


        const confirmTrash = ref(null);
        const handleClickTrash = (id) => {
            confirmTrash.value = id;
        }; handleClickTrash()
        const handleConfirmTrash = () => {
            /* */

            axios.delete(route("locations.trash", confirmTrash.value)).then(response => {
                    setTimeout(() => response($result, 200),10000)
                },
                Inertia.reload(),
     //           location.reload(),
                confirmTrash.value = null
            );
        };



        return { handleClickTrash, confirmTrash, handleConfirmTrash, Inertia,form,reset }; //, fields
    },
    computed: {
        fields() {
            if (this.isClientUser) {
                return ["name", "city", "state", "active"];
            }
            return [
                { name: "client.name", label:"client"},
                "name",
                "city",
                "state",
                "active",
            ];
        },
    },

});
</script>
