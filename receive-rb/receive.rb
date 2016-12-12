#!/usr/bin/env ruby
# encoding: utf-8

STDOUT.sync = true

puts "tac-com online"

#=begin
require "bunny"

conn = Bunny.new(:host => "rabbitmq", :automatically_recover => false)
begin
  conn.start
  puts "we in this bitch"
rescue Bunny::TCPConnectionFailed => e
  puts "stupid dog"
  sleep 1.1
  retry
end
ch = conn.create_channel
q = ch.queue("hello")

begin
  puts "[*] Waiting for messages. To exit press CTRL+C"
  q.subscribe(:block => true) do |delivery_info, properties, body|
    puts "[x] Received #{body}"
  end
rescue Interrupt => _
  conn.close
  exit(0)
end
#=end
