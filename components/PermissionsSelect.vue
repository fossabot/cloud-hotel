<template>
  <v-select
    :loading="loading"
    v-bind="$attrs"
    :items="permissions"
    @change="e => $emit('input', e)"
  />
</template>

<script>
  export default {
    name: "PermissionsSelect",
    data: () => ({
      permissions: [],
      loading: false,
    }),
    async created() {
      try {
        this.loading = true;
        let url = '/roles/permissions'
        const {data:{results}} = await this.$axios.get(url);
        this.permissions = results;
      } finally {
        this.loading = false
      }
    }
  }
</script>

<style scoped>

</style>
