<style>
    .tasklist li.task-high {
        border-left-color: red;
    }
    .tasklist li.task-medium {
        border-left-color: yellow;
    }
    .tasklist li.task-low {
        border-left-color: green;
    }
    .fc-event.task-high {
        border-left: 3px solid red;
    }
    .fc-event.task-medium {
        border-left: 3px solid yellow;
    }
    .fc-event.task-low {
        border-left: 3px solid green;
    }
    .fc-event.disabled {
        opacity: .6;
        text-decoration: line-through !important;
    }
    .fc-event {
        padding: 5px;
        border: none;
        text-align: left;
    }
    .fc-event .fc-content {
        display: flex;
    }
    .fc-event .fc-time{
        flex: 0 0 35px;
    }
    .fc-event .fc-title {
        flex: 1 1 auto;
    }
    .fc-sat, .fc-sun { background-color: #f7f7f7;  }
    .fc-today {
        background-color: #fff5d0;
    }
</style>
