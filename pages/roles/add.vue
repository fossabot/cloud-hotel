<template>
  <v-layout column>
    <v-flex>
      <RoleAddForm/>
    </v-flex>
  </v-layout>
</template>

<script>
  import {mapGetters} from "vuex"
  import RoleAddForm from "~/components/RoleAddForm"

  export default {
    name: "RoleAdd",
    computed: {
      ...mapGetters({
        permissions: "init/PERMISSIONS"
      })
    },
    beforeRouteEnter(from, to, next) {
      next(vm => {
        if(!vm.permissions.includes('user_create')) {
          vm.$router.push('/400')
        }
      })
    },
    components: {RoleAddForm},
  }
</script>

<style scoped>

</style>
