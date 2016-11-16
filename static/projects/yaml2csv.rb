require 'yaml'
require 'csv'

rows = YAML::parse_file('projects.yaml').children

require 'set'
keys = Set.new
rows.each do |row|
  keys += row.value.keys.map(&:value)
end
keys = keys.to_a

CSV.open('projects.csv', 'w') do |f|
  f << keys
  rows.each do |row|
    f << keys.map { | key|
      value = row.at(key)
      value.value if value
    }
  end
end
