<style>
  h4.title {
    margin-top:10px;
  }

  .freez > th {
    top:0;
    position: sticky;
    background-color: white;
    outline: solid 1px #dddddd;
    min-height: 30px;
    height: 30px;
  }

  .tableFixHead tr > td {
    padding: 3px !important;
  }

  select.input-xs {
    padding: 0px 6px;
    border-radius: 0;
  }

  td > select.input-xs {
    border:none;
  }

  td > input.input-xs {
    border:none;
  }

  td > input.input-xs:disabled {
    background-color: white !important;
    color: #555555 !important;
  }


  tr.error > td > input.input-xs, tr.error > td > select.input-xs {
    color:red !important;
  }

  @media (min-width: 768px) {

    .fix-no {
      left: 0;
      position: sticky;
      background-color: #eee !important;
    }

    .fix-check {
      left: 40px;
      position: sticky;
    }

    .fix-add {
      left:80px;
      position: sticky;
    }

    .fix-type {
      left:120px;
      position: sticky;
    }

    .fix-item {
      left:200px;
      position: sticky;
    }    

    td[scope=row] {
      background-color: white;
      border: 0 !important;
      outline: solid 1px #dddddd;
    }
  }
</style>
