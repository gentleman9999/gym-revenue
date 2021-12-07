import { isObject } from "lodash";
import { defaults as defaultTransforms, transforms } from "./transforms";
import {computed} from "vue";

export const getFields = (props) => {
    return computed(()=>{
        let fields = props.fields;
        if(!fields){
            //no fields provided, use keys from data
            fields = Object.keys(props.data);
        }

        fields = fields.map(field=>{
            let _field = field;
            if(!isObject(_field)){
                _field =  {label: field}
            }
            if(!_field.transform){
                // console.log('no transform set for', _field);
                if(_field.label in defaultTransforms){
                    // console.log('found default transform for', _field);
                    _field.transform = defaultTransforms[_field.label];
                }else{
                    // console.log('using noop for', _field);
                    _field.transform = transforms.noop
                    _field.transformNoop = true;
                }
            }else if(typeof _field.transform === 'string'){
                if(_field.transform in transforms){
                    // console.log('found transform for', _field.transform);
                    _field.transform = transforms[_field.transform];
                }else{
                    console.error('Unknown field transform provided', _field);
                    _field.transform = transforms.noop;
                }
            }
            return _field;
        })
        return fields;
    });

};
