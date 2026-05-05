class Category {
  final int id;
  final String name;
  final String slug;
  final String? description;
  final int? parentId;
  final List<Category>? subCategories;

  Category({
    required this.id,
    required this.name,
    required this.slug,
    this.description,
    this.parentId,
    this.subCategories,
  });

  factory Category.fromJson(Map<String, dynamic> json) {
    return Category(
      id: json['id'],
      name: json['name'],
      slug: json['slug'],
      description: json['description'],
      parentId: json['parent_id'],
      subCategories: json['subcategories'] != null
          ? (json['subcategories'] as List)
              .map((i) => Category.fromJson(i))
              .toList()
          : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'slug': slug,
      'description': description,
      'parent_id': parentId,
      'subcategories': subCategories?.map((i) => i.toJson()).toList(),
    };
  }
}
