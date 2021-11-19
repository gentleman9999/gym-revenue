<template>
    <div class="flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="shadow overflow-hidden border-b border-base-100">
                    <slot name="prethead" />
                    <div class="py-4 px-4">
                        <table class="min-w-full divide-y divide-base-100">
                            <thead class="bg-secondary">
                                <tr class="rounded-md">
                                    <th v-for="(header, index) in headers" :key="index" scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">
                                        <p v-if="header !== ''"> {{ header }}</p>
                                        <p v-else class="text-white"><font-awesome-icon :icon="['fas', 'align-left']" size="24"/></p>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-base-300 divide-y divide-base-100">
                            <slot />
                            </tbody>
                        </table>
                    </div>

                    <slot name="pagination" />

                </div>
            </div>
        </div>
    </div>
</template>

<script>

import { library } from '@fortawesome/fontawesome-svg-core';
import { faAlignLeft } from '@fortawesome/pro-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import mapValues from "lodash/mapValues";
library.add(faAlignLeft)

export default {
    name: "GymRevenueTable",
    components: {
        FontAwesomeIcon
    },
    data() {
        return {
            items: 3,
        }
    },
    props: {
        headers: {
            type: Array,
            required: true
        },
    }
}
</script>

<style scoped>

</style>
