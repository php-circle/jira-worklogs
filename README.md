# jira-worklogs

## Installation
`composer global require php-circle/jira-worklogs`

## Usage

Creating work logs:

`jira worklog OP-1479 [HOURS_SPENT] "[CODE_REVIEW]" --datetime="2019-08-02 13:00"`

Get work logs:

`Without parameters, the worklogs within the day will be fetched`

jira workloglist [issue1,issue2,issue3] --from=[date] --to=[date]

e.g.: jira workloglist issue1,issue2,issue --from=2019-01-01
=======
### Create Worklog
`jira worklog OP-1479 [HOURS_SPENT] "[CODE_REVIEW]" --datetime="2019-08-02 13:00"`
