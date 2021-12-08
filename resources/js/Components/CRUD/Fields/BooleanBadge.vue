<template>
    <div class="badge" :class="{'badge-success': value, 'badge-error': !value}">
        {{test() ? truthy :  falsy}}
    </div>
</template>
<script>
import {defineComponent} from 'vue';
export default defineComponent({
    props:{
        value:{
            type: Boolean,
            required: true
        },
        truthy:{
            type: String,
            default: 'true'
        },
        falsy:{
            type: String,
            default: 'false'
        },
        test:{
            type: Function,
        }
    },
    setup(props){
        const __test = () =>{
            return !!props.value;
        }
        const test = props.test || __test;
        return {test};
    }
});
</script>
