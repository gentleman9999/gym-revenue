<template>
    <div class="">
        <ul class="w-full steps">
            <CommsHistoryStep v-for="detail in details" :detail="detail" @click="selectedDetail = detail"/>
        </ul>
        <CommsHistoryDetail :detail="selectedDetail" v-if="selectedDetail"/>
    </div>
</template>
<script>
import {ref} from "vue";
import CommsHistoryStep from "./CommsHistoryStep";
import CommsHistoryDetail from "./CommsHistoryDetail";

export default {
    name: 'CommsHistory',
    components: {
        CommsHistoryDetail,
        CommsHistoryStep
    },
    props: {
        details: {
            type: Array
        }
    },
    setup(props) {
        const selectedDetail = ref(null);

        return {selectedDetail}
    }
}
</script>
