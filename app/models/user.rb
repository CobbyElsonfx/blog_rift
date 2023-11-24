class User < ApplicationRecord
  has_many :posts, foreign_key: :author_id
  has_many :comments
  has_many :likes

  validates :name, presence: true
  validates :photo, presence: true
  validates :bio, presence: true

  def recent_posts(limit = 3)
    posts.order(created_at: :desc).limit(limit)
  end

  def update_posts_counter
    update(posts_counter: posts.count)
  end
end
