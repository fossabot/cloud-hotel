<template>
  <v-layout column>
    <v-flex>
      <v-progress-linear :indeterminate="true" v-if="loading"/>
      <HotelAddForm :hotel="hotel"/>
    </v-flex>
  </v-layout>
</template>

<script>
  import {mapGetters} from "vuex"
  import HotelAddForm from "~/components/HotelAddForm"

  export default {
    name: "HotelAdd",
    computed: {
      ...mapGetters({
        permissions: "init/PERMISSIONS"
      })
    },
    async beforeRouteEnter(from, to, next) {
      await next(async vm => {
        if (!vm.permissions.includes('room_update')) {
          vm.$router.push('/400')
        }
        vm.findHotel(from.params)
      })
    },
    data: () => ({
      hotel: {},
      loading: false
    }),
    components: {HotelAddForm},
    methods: {
      async findHotel({id}) {
        try {
          this.loading = true;
          const {data: {results}} = await this.$axios.get('/hotels/' + id)
          this.hotel = results
        } catch (e) {
          console.log(e)
        } finally {
          this.loading = false
        }
      }
    }
  }
</script>

<style scoped>

</style>
