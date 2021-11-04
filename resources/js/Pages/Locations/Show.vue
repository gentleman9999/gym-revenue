<template>
    <app-layout title="Profile">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Locations
            </h2>
        </template>
        <div class="max-w-7xl mx-auto mb-6 flex justify-between items-center">
<!--            <search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">-->
<!--                <label class="block text-gray-700">Trashed:</label>-->
<!--                <select v-model="form.trashed" class="mt-1 w-full form-select">-->
<!--                    <option :value="null"/>-->
<!--                    <option value="with">With Trashed</option>-->
<!--                    <option value="only">Only Trashed</option>-->
<!--                </select>-->
<!--            </search-filter>-->
            <Link class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition" :href="route('locations.create')">
                <span>Create</span>
                <span class="hidden md:inline">Location</span>
            </Link>
        </div>
        <div class="max-w-7xl mx-auto bg-white rounded-md shadow overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <tr class="text-left font-bold">
                    <th class="px-6 pt-6 pb-4">Name</th>
                </tr>
                <tr v-for="location in locations" :key="location.id"
                    class="hover:bg-gray-100 focus-within:bg-gray-100">
                    <td class="border-t">
                        <Link class="px-6 py-4 flex items-center focus:text-indigo-500"
                                      :href="route('locations.update', location.id)">
                            {{ location.name }}
                            <icon v-if="location.deleted_at" name="trash"
                                  class="flex-shrink-0 w-3 h-3 fill-gray-400 ml-2"/>
                        </Link>
                    </td>

                    <td class="border-t w-px">
                        <Link class="px-4 flex items-center" :href="route('locations.update', location.id)"
                                      tabindex="-1">
<!--                            <icon name="cheveron-right" class="block w-6 h-6 fill-gray-400"/>-->
                        </Link>
                    </td>
                </tr>
                <tr v-if="locations.length === 0">
                    <td class="border-t px-6 py-4" colspan="4">No Locations found.</td>
                </tr>
            </table>
        </div>
<!--        <pagination class="mt-6" :links="locations.links"/>-->
    </app-layout>
</template>

<script>
import {defineComponent} from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import JetSectionBorder from '@/Jetstream/SectionBorder.vue'
import Button from '@/Jetstream/Button.vue'
import { Link } from '@inertiajs/inertia-vue3';


export default defineComponent({
    props: ['sessions', 'locations'],

    components: {
        AppLayout,
        JetSectionBorder,
        Link,
        Button
    },
})
</script>
