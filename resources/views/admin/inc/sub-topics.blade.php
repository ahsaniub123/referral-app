<div class="mb-3">
    <label class="form-label">Choose Sub Topic</label>
    <select required name="sub_topic_id" id="" class="js-sub-topic-select form-control">
        <option disabled selected>Select from our category</option>
        @foreach($sub_topics as $topic)
            <option value="{{ $topic->id }}">{{ $topic->title }}</option>
        @endforeach
    </select>
</div>

<div class="js-article-titles">
    <div class="mb-3">
        <label class="form-label">Choose Article</label>
        <select required name="article_title_id" id="" class="form-control">
            <option disabled selected>Select from our category</option>
            @foreach($article_titles as $title)
                <option value="{{ $title->id }}">{{ $title->title }}</option>
            @endforeach
        </select>
    </div>
</div>
