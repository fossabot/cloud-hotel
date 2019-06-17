<template>
  <v-card>
    <v-card-text>
      <v-layout row>
        <v-flex sm4>
          <Upload v-model="form.image" :_preview="form.image" :accept="['image/jpeg', 'image/png']"/>
        </v-flex>
        <v-flex sm8>
          <v-text-field
            v-model.trim="form.name"
            placeholder="Name"
            :error-messages="formError.name"
            :error="formError.name.length > 0"
          />
          <RoomTypeSelect
            v-model.trim="form.type_id"
            placeholder="Room Types"
            :error-messages="formError.type_id"
            :error="formError.type_id.length > 0"
          />
          <RoomCapacitySelect
            v-model.trim="form.capacity_id"
            placeholder="Room Capacity"
            :error-messages="formError.capacity_id"
            :error="formError.capacity_id.length > 0"
          />
        </v-flex>
      </v-layout>
    </v-card-text>
    <v-divider/>
    <v-card-actions>
      <v-btn v-if="edit === false" :disabled="loading" @click.prevent.stop="submit" color="primary">
        Submit
      </v-btn>
      <v-btn v-if="edit === true" :disabled="loading" @click.prevent.stop="update" color="primary">
        Update
      </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
  import RoomTypeSelect from "./RoomTypeSelect";
  import RoomCapacitySelect from "./RoomCapacitySelect";
  import Upload from "./Upload";

  export default {
    name: "RoomAddForm",
    components: {Upload, RoomCapacitySelect, RoomTypeSelect},
    props: {
      role: {
        type: Object,
        default: () => ({})
      }
    },
    data: () => ({
      loading: false,
      form: {
        name: "",
        type_id: '',
        capacity_id: '',
        image: ''
      },
      formError: {
        name: [],
        type_id: [],
        capacity_id: [],
        image: []
      },
      edit: false
    }),
    watch: {
      role(e) {
        if (Object.keys(e).length > 0) {
          this.form = {
            ...this.form,
            name: e.name,
            type_id: e.type_id,
            capacity_id: e.capacity_id
          }
          this.edit = true
        }
      }
    },
    methods: {
      async submit() {
        try {
          this.loading = true;
          this.formError = {
            name: [],
            type_id: [],
            capacity_id: []
          }
          let form = new FormData();
          Object.keys(this.form)
            .forEach(e => form.append(e, this.form[e]))
          await this.$axios.post('/rooms', form)
          this.$router.push('/rooms')
          this.$toast.success("Room create successful")
        } catch (e) {
          if (e.response) {
            const code = parseInt(e.response.status);
            if (!!code && code === 422) {
              const data = e.response.data;
              if (data) {
                const errors = data.errors;
                if (errors) {
                  this.formError = {
                    ...this.formError,
                    ...errors
                  }
                }
              }

            }
          }
        } finally {
          this.loading = false
        }
      },
      async update() {
        try {
          this.loading = true;
          this.formError = {
            name: [],
            type_id: [],
            capacity_id: []
          }
          await this.$axios.$put('/rooms/' + this.role.id, this.form);
          this.$router.push('/rooms/edit/' + this.role.id)
          this.$toast.success("Update successful")
        } catch (e) {
          if (e.response) {
            const code = parseInt(e.response.status);
            if (!!code && code === 422) {
              const data = e.response.data;
              if (data) {
                const errors = data.errors;
                if (errors) {
                  this.formError = {
                    ...this.formError,
                    ...errors
                  }
                }
              }

            }
          }
        } finally {
          this.loading = false
        }
      }
    }
  }
</script>

<style scoped>

</style>
