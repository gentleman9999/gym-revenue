<template>
    <div class="flex flex-col  m">
        <div class="-my-2 sm:-mx-6 lg:-mx-8 ax-w-screen overflow-x-scroll">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8 ">
                <div class="shadow border-b border-base-100 bg-base-300 ">
                    <slot name="prethead"/>
                    <div class="py-4 px-4">
                        <table class="table w-full" :class="{'table-zebra': zebra}">
                            <thead>
                            <tr>
                                <th v-for="(header, index) in headers" :key="index" scope="col">
                                    <p v-if="header !== ''"> {{ header }}</p>
                                    <p v-else class="text-white">
                                        <font-awesome-icon :icon="['fas', 'align-left']" size="24"/>
                                    </p>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <slot/>
                            </tbody>
                        </table>
                    </div>

                    <slot name="pagination"/>

                </div>
            </div>
        </div>
    </div>
</template>

<script>

import {library} from '@fortawesome/fontawesome-svg-core';
import {faAlignLeft} from '@fortawesome/pro-solid-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome';

library.add(faAlignLeft)

export default {
    name: "GymRevenueTable",
    components: {
        FontAwesomeIcon
    },
    props: {
        headers: {
            type: Array,
            required: true
        },
        zebra:{
            type: Boolean,
            default: true,
        }
    }
}
</script>
